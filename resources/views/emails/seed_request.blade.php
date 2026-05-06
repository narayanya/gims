<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Seed Request Notification</title>
</head>
<body style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; background-color:#eef2f7; color:#333;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding:30px 0; background-color:#eef2f7;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
          
          <!-- Header with Accent -->
          <tr>
            <td style="background-color:#b0cfb9; padding:20px; text-align:center;">
              <img src="email-logo.png" alt="Gims" style="max-height:60px; display:block; margin:0 auto;">
              <h3 style="color:#000000; margin:10px 0 0; font-size:18px; font-weight:normal;">Seed Request</h3>
            </td>
          </tr>
          <tr>
            <td style="padding:25px; font-size:14px; line-height:1.6; color:#444;">
              <p style="margin:0 0 15px;">Dear <strong>{{ $data['reporting_user'] }}</strong>,</p>

              <p style="margin:0 0 15px;">We would like to inform you that your team member <strong>{{ $data['requester_name'] }}</strong> has requested a seed request for <strong>recharch</strong>.</p>

              <p style="margin:0 0 20px;"> Kindly check and take necessary action.</p>
            
              <table width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse; font-size:14px;">
                <tr>
                  <td colspan="3" style="background-color:#f4f8fc; padding:10px; font-weight:bold; border-radius:4px 4px 0 0; color:#0066cc; border:1px solid #dce6f1;">Seed Request Details</td>
                </tr>
                <tr>
                  <td style="background-color:#fafafa; border:1px solid #e6e6e6; padding:10px; width:35%;"><strong>Crop</strong></td>
                  <td style="background-color:#fafafa; border:1px solid #e6e6e6; padding:10px; width:35%;"><strong>Quantity</strong></td>
                  <td style="background-color:#fafafa; border:1px solid #e6e6e6; padding:10px; width:35%;"><strong>Unit</strong></td>
                </tr>
                @foreach($data['rows'] as $row)
                <tr>
                  <td style="border:1px solid #e6e6e6; padding:10px;">{{ $row['crop'] }}</td>
                  <td style="border:1px solid #e6e6e6; padding:10px;">{{ $row['quantity'] }}</td>
                    <td style="border:1px solid #e6e6e6; padding:10px;">{{ $row['unit'] }}</td>
                </tr>
                @endforeach
                </table>
                <p style="margin:20px 0 0;"><strong>Purpose:</strong> {{ $data['purpose'] }}</p>
                <p style="margin:10px 0 0;"><strong>Details:</strong> {{ $data['purpose_details'] }}</p>
                <p style="margin:30px 0 0;">Best regards,<br>Gims Team</p>
            </td>
            </tr>
        </table>
      </td>
    </tr>
  </table> 

</body>
</html>