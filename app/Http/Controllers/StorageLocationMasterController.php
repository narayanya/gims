<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Rack;
use App\Models\Bin;
use App\Models\Container;
use App\Models\Unit;
use Illuminate\Http\Request;

class StorageLocationMasterController extends Controller
{
    public function index()
    {
        return view('master.storage-location-master.index', [
            'sections'   => Section::orderBy('name')->paginate(10, ['*'], 'sections_page'),
            'racks'      => Rack::with('section')->orderBy('name')->paginate(10, ['*'], 'racks_page'),
            'bins'       => Bin::with('rack')->orderBy('name')->paginate(10, ['*'], 'bins_page'),
            'containers' => Container::orderBy('name')->paginate(10, ['*'], 'containers_page'),
            'allSections'=> Section::where('status',1)->orderBy('name')->get(),
            'allRacks'   => Rack::where('status',1)->orderBy('name')->get(),
            'units'      => Unit::where('status',1)->orderBy('name')->get(),
        ]);
    }

    // ── Section ──────────────────────────────────────────────────────────
    public function sectionStore(Request $request)
    {
        

        $request->validate(['name'=>'required|string|max:255|unique:sections,name',
        'code'=>'nullable|string|max:50|unique:sections,code', 'unit_id'=>'required|exists:units,id']);
        Section::create($request->only('name','code','unit_id','description','status'));
        return back()->with('success','Section added.');
    }
    public function sectionUpdate(Request $request, Section $section)
    {
        $request->validate(['name'=>'required|string|max:255|unique:sections,name,'.$section->id,'code'=>'nullable|string|max:50|unique:sections,code,'.$section->id]);
        $section->update($request->only('name','code','description','status'));
        return back()->with('success','Section updated.');
    }
    public function sectionDestroy(Section $section)
    {
        $section->update(['status'=>0]);
        return back()->with('success','Section deactivated.');
    }

    // ── Rack ─────────────────────────────────────────────────────────────
    public function rackStore(Request $request)
    {
        $request->validate(['name'=>'required|string|max:255|unique:racks,name','code'=>'nullable|string|max:50|unique:racks,code']);
        Rack::create($request->only('name','code','section_id','description','status'));
        return back()->with('success','Rack added.');
    }
    public function rackUpdate(Request $request, Rack $rack)
    {
        $request->validate(['name'=>'required|string|max:255|unique:racks,name,'.$rack->id,'code'=>'nullable|string|max:50|unique:racks,code,'.$rack->id]);
        $rack->update($request->only('name','code','section_id','description','status'));
        return back()->with('success','Rack updated.');
    }
    public function rackDestroy(Rack $rack)
    {
        $rack->update(['status'=>0]);
        return back()->with('success','Rack deactivated.');
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

    // ── Container ────────────────────────────────────────────────────────
    public function containerStore(Request $request)
    {
        $request->validate(['name'=>'required|string|max:255|unique:containers,name','code'=>'nullable|string|max:50|unique:containers,code']);
        Container::create($request->only('name','code','container_type','capacity','description','status'));
        return back()->with('success','Container added.');
    }
    public function containerUpdate(Request $request, Container $container)
    {
        $request->validate(['name'=>'required|string|max:255|unique:containers,name,'.$container->id,'code'=>'nullable|string|max:50|unique:containers,code,'.$container->id]);
        $container->update($request->only('name','code','container_type','capacity','description','status'));
        return back()->with('success','Container updated.');
    }
    public function containerDestroy(Container $container)
    {
        $container->update(['status'=>0]);
        return back()->with('success','Container deactivated.');
    }
}
