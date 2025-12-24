<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WhatsappMessage;
use App\Models\AgentWorkLog;
use App\Models\User;
use App\Models\WhatsappDevice;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function general(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30));
        $endDate = $request->input('end_date', now());
        
        // Total messages received and replied
        $totalReceived = WhatsappMessage::whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        $totalReplied = WhatsappMessage::where('replied', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        // Average response time
        $avgResponseTime = WhatsappMessage::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('response_time_minutes')
            ->avg('response_time_minutes');
        
        // Messages by group
        $messagesByGroup = WhatsappMessage::whereBetween('created_at', [$startDate, $endDate])
            ->select('group_type', DB::raw('COUNT(*) as count'))
            ->whereNotNull('group_type')
            ->groupBy('group_type')
            ->get();
        
        // Messages by device
        $messagesByDevice = WhatsappMessage::whereBetween('created_at', [$startDate, $endDate])
            ->join('whatsapp_devices', 'whatsapp_messages.device_id', '=', 'whatsapp_devices.id')
            ->select('whatsapp_devices.device_name', DB::raw('COUNT(*) as count'))
            ->groupBy('whatsapp_devices.device_name')
            ->get();
        
        // Daily message trend
        $dailyTrend = WhatsappMessage::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return view('admin.reports.general', compact(
            'totalReceived',
            'totalReplied',
            'avgResponseTime',
            'messagesByGroup',
            'messagesByDevice',
            'dailyTrend',
            'startDate',
            'endDate'
        ));
    }

    public function agents(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30));
        $endDate = $request->input('end_date', now());
        $agentId = $request->input('agent_id');
        
        $query = AgentWorkLog::with('user')
            ->whereBetween('login_at', [$startDate, $endDate]);
        
        if ($agentId) {
            $query->where('user_id', $agentId);
        }
        
        $workLogs = $query->get();
        
        // Agent statistics
        $agentStats = User::where('role', 'employee')
            ->with(['workLogs' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('login_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function($agent) {
                $logs = $agent->workLogs;
                return [
                    'id' => $agent->id,
                    'name' => $agent->name,
                    'total_hours' => $logs->sum('work_hours'),
                    'messages_replied' => $logs->sum('messages_replied'),
                    'auto_transferred' => $logs->sum('messages_auto_transferred'),
                    'manual_transferred' => $logs->sum('messages_manual_transferred'),
                    'avg_response_time' => $logs->avg('avg_response_time'),
                    'sessions_count' => $logs->count(),
                ];
            });
        
        $agents = User::where('role', 'employee')->get();
        
        return view('admin.reports.agents', compact(
            'workLogs',
            'agentStats',
            'agents',
            'startDate',
            'endDate',
            'agentId'
        ));
    }

    public function agentDetail(Request $request, $userId)
    {
        $startDate = $request->input('start_date', now()->subDays(30));
        $endDate = $request->input('end_date', now());
        
        $agent = User::findOrFail($userId);
        
        $workLogs = AgentWorkLog::where('user_id', $userId)
            ->whereBetween('login_at', [$startDate, $endDate])
            ->orderBy('login_at', 'desc')
            ->get();
        
        // Messages handled by agent
        $messagesHandled = WhatsappMessage::where('assigned_to', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        
        $stats = [
            'total_hours' => $workLogs->sum('work_hours'),
            'total_sessions' => $workLogs->count(),
            'messages_replied' => $workLogs->sum('messages_replied'),
            'auto_transferred' => $workLogs->sum('messages_auto_transferred'),
            'manual_transferred' => $workLogs->sum('messages_manual_transferred'),
            'avg_response_time' => $workLogs->avg('avg_response_time'),
            'messages_per_hour' => $workLogs->sum('work_hours') > 0 
                ? round($workLogs->sum('messages_replied') / $workLogs->sum('work_hours'), 2)
                : 0,
        ];
        
        return view('admin.reports.agent-detail', compact(
            'agent',
            'workLogs',
            'messagesHandled',
            'stats',
            'startDate',
            'endDate'
        ));
    }

    public function exportGeneral(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30));
        $endDate = $request->input('end_date', now());
        
        $data = $this->getGeneralReportData($startDate, $endDate);
        
        return $this->exportToExcel($data, 'general-report');
    }

    public function exportAgents(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30));
        $endDate = $request->input('end_date', now());
        
        $data = $this->getAgentsReportData($startDate, $endDate);
        
        return $this->exportToExcel($data, 'agents-report');
    }

    private function getGeneralReportData($startDate, $endDate)
    {
        return [
            'summary' => [
                ['Metric', 'Value'],
                ['Total Received Messages', WhatsappMessage::whereBetween('created_at', [$startDate, $endDate])->count()],
                ['Total Replied Messages', WhatsappMessage::where('replied', true)->whereBetween('created_at', [$startDate, $endDate])->count()],
                ['Average Response Time (minutes)', round(WhatsappMessage::whereBetween('created_at', [$startDate, $endDate])->whereNotNull('response_time_minutes')->avg('response_time_minutes'), 2)],
            ],
            'by_group' => WhatsappMessage::whereBetween('created_at', [$startDate, $endDate])
                ->select('group_type', DB::raw('COUNT(*) as count'))
                ->whereNotNull('group_type')
                ->groupBy('group_type')
                ->get()
                ->map(fn($item) => [$item->group_type, $item->count])
                ->prepend(['Group Type', 'Message Count'])
                ->toArray(),
        ];
    }

    private function getAgentsReportData($startDate, $endDate)
    {
        $agents = User::where('role', 'employee')
            ->with(['workLogs' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('login_at', [$startDate, $endDate]);
            }])
            ->get();
        
        $data = [['Agent Name', 'Total Hours', 'Messages Replied', 'Auto Transferred', 'Manual Transferred', 'Avg Response Time']];
        
        foreach ($agents as $agent) {
            $logs = $agent->workLogs;
            $data[] = [
                $agent->name,
                $logs->sum('work_hours'),
                $logs->sum('messages_replied'),
                $logs->sum('messages_auto_transferred'),
                $logs->sum('messages_manual_transferred'),
                round($logs->avg('avg_response_time'), 2),
            ];
        }
        
        return ['agents' => $data];
    }

    private function exportToExcel($data, $filename)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $row = 1;
        foreach ($data as $sheetName => $sheetData) {
            foreach ($sheetData as $rowData) {
                $col = 'A';
                foreach ($rowData as $cellData) {
                    $sheet->setCellValue($col . $row, $cellData);
                    $col++;
                }
                $row++;
            }
            $row++;
        }
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $filename = $filename . '-' . date('Y-m-d') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);
        
        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
