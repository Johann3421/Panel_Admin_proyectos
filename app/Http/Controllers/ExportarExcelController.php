<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\DB;

class ExportarExcelController extends Controller
{
    public function exportar()
    {
        // Crear una nueva hoja de cálculo
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Escribir los encabezados
        $sheet->setCellValue('A1', 'Nro.')
              ->setCellValue('B1', 'Fecha de visita')
              ->setCellValue('C1', 'Visitante')
              ->setCellValue('D1', 'Documento del visitante')
              ->setCellValue('E1', 'Hora Ingreso')
              ->setCellValue('F1', 'Hora Salida')
              ->setCellValue('G1', 'Motivo')
              ->setCellValue('H1', 'Lugar Específico');

        // Obtener los datos de la tabla visitas
        $visitas = DB::table('visitas')->get();
        $nro = 1;
        $rowIndex = 2; // Comienza en la fila 2

        foreach ($visitas as $visita) {
            $sheet->setCellValue('A' . $rowIndex, $nro++)
                  ->setCellValue('B' . $rowIndex, $visita->fecha)
                  ->setCellValue('C' . $rowIndex, $visita->nombre)
                  ->setCellValue('D' . $rowIndex, $visita->dni)
                  ->setCellValue('E' . $rowIndex, $visita->hora_ingreso)
                  ->setCellValue('F' . $rowIndex, $visita->hora_salida)
                  ->setCellValue('G' . $rowIndex, $visita->smotivo)
                  ->setCellValue('H' . $rowIndex, $visita->lugar);
            $rowIndex++;
        }

        // Configurar los encabezados HTTP para la descarga del archivo
        $fileName = 'visitas_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        // Crear el archivo Excel y enviarlo al navegador
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
