<?php

namespace App\Http\Controllers;

use App\Exports\ExportFactory;
use App\Http\Requests\ExportRequest;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    protected $exportFactory;

    public function __construct(ExportFactory $exportFactory)
    {
        $this->exportFactory = $exportFactory;
    }

    public function export(ExportRequest $request)
    {
        $entity = $request->input('entity');
        $export = $this->exportFactory->make($entity);
        $filename = $entity . '_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download($export, $filename);
    }
}
