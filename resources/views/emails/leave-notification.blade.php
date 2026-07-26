<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.05);">
                    {{-- Header --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#4f46e5,#7c3aed);padding:32px 40px;text-align:center;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:12px;display:inline-block;line-height:48px;font-size:24px;">
                                            📋
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top:12px;">
                                        <h1 style="color:#ffffff;margin:0;font-size:22px;font-weight:700;">Sistem Pengajuan Cuti</h1>
                                        <p style="color:rgba(255,255,255,0.8);margin:4px 0 0;font-size:13px;">PT. Company</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:32px 40px;">
                            @if($type === 'submitted')
                                <h2 style="color:#1f2937;font-size:18px;margin:0 0 16px;">Pengajuan Cuti Baru</h2>
                                <p style="color:#6b7280;font-size:14px;line-height:1.6;margin:0 0 20px;">
                                    Halo <strong>{{ $manager->name ?? 'Manager' }}</strong>,
                                </p>
                                <p style="color:#6b7280;font-size:14px;line-height:1.6;margin:0 0 20px;">
                                    <strong>{{ $leaveRequest->user->name }}</strong> telah mengajukan pengajuan cuti baru yang memerlukan persetujuan Anda.
                                </p>
                                <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f9fafb;border-radius:8px;border:1px solid #e5e7eb;margin:0 0 24px;">
                                    <tr>
                                        <td style="padding:20px;">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="padding:6px 0;color:#6b7280;font-size:13px;width:140px;">Karyawan</td>
                                                    <td style="padding:6px 0;color:#1f2937;font-size:13px;font-weight:600;">{{ $leaveRequest->user->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:6px 0;color:#6b7280;font-size:13px;">Jenis Cuti</td>
                                                    <td style="padding:6px 0;color:#1f2937;font-size:13px;font-weight:600;">{{ $leaveRequest->leaveType->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:6px 0;color:#6b7280;font-size:13px;">Periode</td>
                                                    <td style="padding:6px 0;color:#1f2937;font-size:13px;font-weight:600;">{{ $leaveRequest->start_date->format('d/m/Y') }} - {{ $leaveRequest->end_date->format('d/m/Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:6px 0;color:#6b7280;font-size:13px;">Jumlah Hari</td>
                                                    <td style="padding:6px 0;color:#1f2937;font-size:13px;font-weight:600;">{{ $leaveRequest->total_days }} hari</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:6px 0;color:#6b7280;font-size:13px;">Alasan</td>
                                                    <td style="padding:6px 0;color:#1f2937;font-size:13px;">{{ $leaveRequest->reason }}</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td align="center">
                                            <a href="{{ url('/admin/leave-requests/' . $leaveRequest->id) }}" style="display:inline-block;background-color:#4f46e5;color:#ffffff;font-size:14px;font-weight:600;padding:12px 32px;border-radius:8px;text-decoration:none;">
                                                Tinjau Pengajuan
                                            </a>
                                        </td>
                                    </tr>
                                </table>

                            @elseif($type === 'approved')
                                <h2 style="color:#059669;font-size:18px;margin:0 0 16px;">Pengajuan Cuti Disetujui</h2>
                                <p style="color:#6b7280;font-size:14px;line-height:1.6;margin:0 0 20px;">
                                    Halo <strong>{{ $leaveRequest->user->name }}</strong>,
                                </p>
                                <p style="color:#6b7280;font-size:14px;line-height:1.6;margin:0 0 20px;">
                                    Pengajuan cuti Anda telah <strong style="color:#059669;">disetujui</strong>.
                                </p>
                                <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;margin:0 0 24px;">
                                    <tr>
                                        <td style="padding:20px;">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="padding:6px 0;color:#6b7280;font-size:13px;width:140px;">Jenis Cuti</td>
                                                    <td style="padding:6px 0;color:#1f2937;font-size:13px;font-weight:600;">{{ $leaveRequest->leaveType->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:6px 0;color:#6b7280;font-size:13px;">Periode</td>
                                                    <td style="padding:6px 0;color:#1f2937;font-size:13px;font-weight:600;">{{ $leaveRequest->start_date->format('d/m/Y') }} - {{ $leaveRequest->end_date->format('d/m/Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:6px 0;color:#6b7280;font-size:13px;">Jumlah Hari</td>
                                                    <td style="padding:6px 0;color:#1f2937;font-size:13px;font-weight:600;">{{ $leaveRequest->total_days }} hari</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>

                            @elseif($type === 'rejected')
                                <h2 style="color:#dc2626;font-size:18px;margin:0 0 16px;">Pengajuan Cuti Ditolak</h2>
                                <p style="color:#6b7280;font-size:14px;line-height:1.6;margin:0 0 20px;">
                                    Halo <strong>{{ $leaveRequest->user->name }}</strong>,
                                </p>
                                <p style="color:#6b7280;font-size:14px;line-height:1.6;margin:0 0 20px;">
                                    Pengajuan cuti Anda telah <strong style="color:#dc2626;">ditolak</strong>.
                                </p>
                                @if($leaveRequest->rejection_reason)
                                <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fef2f2;border-radius:8px;border:1px solid #fecaca;margin:0 0 24px;">
                                    <tr>
                                        <td style="padding:20px;">
                                            <p style="color:#991b1b;font-size:13px;font-weight:600;margin:0 0 4px;">Alasan Penolakan:</p>
                                            <p style="color:#7f1d1d;font-size:14px;margin:0;">{{ $leaveRequest->rejection_reason }}</p>
                                        </td>
                                    </tr>
                                </table>
                                @endif
                            @endif
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color:#f9fafb;padding:24px 40px;border-top:1px solid #e5e7eb;">
                            <p style="color:#9ca3af;font-size:12px;margin:0;text-align:center;line-height:1.5;">
                                Email ini dikirim otomatis oleh Sistem Pengajuan Cuti PT. Company<br>
                                Jika Anda memiliki pertanyaan, hubungi tim HR di hr@company.com
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
