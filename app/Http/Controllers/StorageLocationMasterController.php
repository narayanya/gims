<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Rack;
use App\Models\Bin;
use App\Models\Container;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Models\StorageLocation;
use App\Models\Warehouse;
use App\Exports\StorageLocationMasterExport;
use App\Imports\RackImport;
use App\Imports\BinImport;
use App\Imports\ContainerImport;
use Maatwebsite\Excel\Facades\Excel;


class StorageLocationMasterController extends Controller
{
    public function index()
    {
        return view('master.storage-location-master.index', [
            'racks'      => Rack::with(['warehouse', 'storage.warehouse'])->orderBy('name')->paginate(10, ['*'], 'racks_page'),
            'bins'       => Bin::with('rack')->orderBy('name')->paginate(10, ['*'], 'bins_page'),
            'containers' => Container::with(['bin', 'rack'])->orderBy('name')->paginate(10, ['*'], 'containers_page'),
            'allSections'=> Section::where('status',1)->orderBy('name')->get(),
            'allRacks'   => Rack::where('status',1)->orderBy('name')->get(),
            'allBins'    => Bin::where('status',1)->orderBy('name')->get(['id','name','rack_id']),
            'units'      => Unit::where('status',1)->orderBy('name')->get(),
            'warehouses' => Warehouse::where('status', 1)->orderBy('name')->get(),
            'allStorages' => \App\Models\Storage::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    // ── Section ──────────────────────────────────────────────────────────
    public function sectionStore(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255|unique:sections,name',
            'code'       => 'nullable|string|max:50|unique:sections,code',
            'unit_id'    => 'required|exists:units,id',
            'storage_id' => 'nullable|exists:storages,id',
        ]);
        Section::create($request->only('name', 'code', 'unit_id', 'storage_id', 'description', 'status'));
        return back()->with('success', 'Section added.');
    }
    public function sectionUpdate(Request $request, Section $section)
    {
        $request->validate([
            'name'       => 'required|string|max:255|unique:sections,name,'.$section->id,
            'code'       => 'nullable|string|max:50|unique:sections,code,'.$section->id,
            'storage_id' => 'nullable|exists:storages,id',
        ]);
        $section->update($request->only('name', 'code', 'storage_id', 'description', 'status'));
        return back()->with('success', 'Section updated.');
    }
    public function sectionDestroy(Section $section)
    {
        $section->update(['status'=>0]);
        return back()->with('success','Section deactivated.');
    }

    // ── Rack ─────────────────────────────────────────────────────────────
    public function rackStore(Request $request)
    {
        $request->validate(['name'=>'required|string|max:255|unique:racks,name', 'code'=>'nullable|string|max:50|unique:racks,code']);

        $data = $request->only('name','code','storage_id','description','status');

        // Derive warehouse_id from storage if not explicitly provided
        $data['warehouse_id'] = $request->warehouse_id
            ?: ($request->storage_id ? \App\Models\Storage::find($request->storage_id)?->warehouse_id : null);

        Rack::create($data);
        return back()->with('success','Rack added.');
    }

    public function rackUpdate(Request $request, Rack $rack)
    {
        $request->validate(['name'=>'required|string|max:255|unique:racks,name,'.$rack->id,'code'=>'nullable|string|max:50|unique:racks,code,'.$rack->id]);

        $data = $request->only('name','code','storage_id','description','status');

        // Derive warehouse_id from storage if not explicitly provided
        $data['warehouse_id'] = $request->warehouse_id
            ?: ($request->storage_id ? \App\Models\Storage::find($request->storage_id)?->warehouse_id : null);

        $rack->update($data);
        return back()->with('success','Rack updated.');
    }

    public function rackDestroy(Rack $rack)
    {
        $rack->update(['status'=>0]);
        return back()->with('success','Rack deactivated.');
    }

    public function rackDelete(Rack $rack)
    {
        // Check lots using this rack
        $lotCount = \App\Models\Lot::where('rack_id', $rack->id)->count();
        if ($lotCount > 0) {
            return back()->with('error',
                "Cannot delete rack \"{$rack->name}\" — {$lotCount} lot(s) are assigned to it."
            );
        }

        // Check lot_transfers FK
        $transferCount = \Illuminate\Support\Facades\DB::table('lot_transfers')
            ->where('from_rack_id', $rack->id)
            ->orWhere('to_rack_id', $rack->id)
            ->count();
        if ($transferCount > 0) {
            return back()->with('error',
                "Cannot delete rack \"{$rack->name}\" — it is used in {$transferCount} lot transfer(s)."
            );
        }

        // Deassign any bins linked to this rack before deleting
        Bin::where('rack_id', $rack->id)->update(['rack_id' => null]);

        $rack->delete();
        return back()->with('success', "Rack \"{$rack->name}\" deleted successfully.");
    }

    // ── Bin ──────────────────────────────────────────────────────────────
    public function binStore(Request $request)
    {
        $request->validate(['name'=>'required|string|max:255|unique:bins,name','code'=>'nullable|string|max:50|unique:bins,code']);
        Bin::create($request->only('name','code','rack_id','description','status'));
        return back()->with('success','Bin added.');
    }

    public function binUpdate(Request $request, Bin $bin)
    {
        $request->validate(['name'=>'required|string|max:255|unique:bins,name,'.$bin->id,'code'=>'nullable|string|max:50|unique:bins,code,'.$bin->id]);
        $bin->update($request->only('name','code','rack_id','description','status'));
        return back()->with('success','Bin updated.');
    }

    public function binDestroy(Bin $bin)
    {
        $bin->update(['status'=>0]);
        return back()->with('success','Bin deactivated.');
    }

    public function binDelete(Bin $bin)
    {
        // Check lots using this bin
        $lotCount = \App\Models\Lot::where('bin_id', $bin->id)->count();
        if ($lotCount > 0) {
            return back()->with('error',
                "Cannot delete bin \"{$bin->name}\" — {$lotCount} lot(s) are assigned to it."
            );
        }

        // Check lot_transfers FK
        $transferCount = \Illuminate\Support\Facades\DB::table('lot_transfers')
            ->where('from_bin_id', $bin->id)
            ->orWhere('to_bin_id', $bin->id)
            ->count();
        if ($transferCount > 0) {
            return back()->with('error',
                "Cannot delete bin \"{$bin->name}\" — it is used in {$transferCount} lot transfer(s)."
            );
        }

        $bin->delete();
        return back()->with('success', "Bin \"{$bin->name}\" deleted successfully.");
    }

    // ── Container ────────────────────────────────────────────────────────
    public function containerStore(Request $request)
    {
        $request->validate(['name'=>'required|string|max:255|unique:containers,name','code'=>'nullable|string|max:50|unique:containers,code', 'unit_id' => 'nullable|exists:units,id',]);
        Container::create($request->only('name','code','container_type','capacity', 'length', 'width', 'height', 'dimension_unit', 'unit_id', 'rack_id', 'bin_id', 'description','status'));
        return back()->with('success','Container added.');
    }

    public function containerUpdate(Request $request, Container $container)
    {
        $request->validate(['name'=>'required|string|max:255|unique:containers,name,'.$container->id,'code'=>'nullable|string|max:50|unique:containers,code,'.$container->id]);
        $container->update($request->only('name','code','container_type','capacity', 'length', 'width', 'height', 'dimension_unit', 'unit_id', 'rack_id', 'bin_id', 'description','status'));
        return back()->with('success','Container updated.');
    }

    public function containerDestroy(Container $container)
    {
        $container->update(['status'=>0]);
        return back()->with('success','Container deactivated.');
    }

    public function containerDelete(Container $container)
    {
        // Check lots using this container
        $lotCount = \App\Models\Lot::where('container_id', $container->id)->count();
        if ($lotCount > 0) {
            return back()->with('error',
                "Cannot delete container \"{$container->name}\" — {$lotCount} lot(s) are assigned to it."
            );
        }

        $container->delete();
        return back()->with('success', "Container \"{$container->name}\" deleted successfully.");
    }

    public function export()
    {
        return Excel::download(
            new StorageLocationMasterExport,
            'storage-location-master-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    // ── Import ───────────────────────────────────────────────────────────

    public function rackImport(\Illuminate\Http\Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,xlsx,xls']);
        Excel::import(new RackImport, $request->file('file'));
        return redirect()->route('storage-location-master.index', ['tab' => 'rack'])
            ->with('success', 'Racks imported successfully.');
    }

    public function binImport(\Illuminate\Http\Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,xlsx,xls']);
        Excel::import(new BinImport, $request->file('file'));
        return redirect()->route('storage-location-master.index', ['tab' => 'bin'])
            ->with('success', 'Bins imported successfully.');
    }

    public function containerImport(\Illuminate\Http\Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,xlsx,xls']);
        Excel::import(new ContainerImport, $request->file('file'));
        return redirect()->route('storage-location-master.index', ['tab' => 'container'])
            ->with('success', 'Containers imported successfully.');
    }
}
