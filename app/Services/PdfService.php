<?php

namespace App\Services;

use App\Models\OrdreReparation;
use App\Models\Parametre;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;

/**
 * Service de génération de PDF (devis / facture).
 *
 * Prérequis : composer require barryvdh/laravel-dompdf
 */
class PdfService
{
    /**
     * Génère le PDF d'un devis ou d'une facture.
     *
     * @return mixed PDF DomPDF, ou Response HTML en fallback
     */
    public function genererOrdre(OrdreReparation $ordre, string $type = 'devis'): mixed
    {
        $ordre->load(['client', 'vehicule', 'lignes.piece', 'createdBy']);

        $titre = $type === 'facture' ? 'FACTURE' : 'DEVIS';
        $numero = $ordre->numero;

        $atelier = [
            'nom' => Parametre::get('atelier.nom', config('app.name', 'Garage Moderne')),
            'adresse' => Parametre::get('atelier.adresse', config('garage.adresse', 'Bamako, Mali')),
            'telephone' => Parametre::get('atelier.telephone', config('garage.telephone', '+223 70 00 00 00')),
            'email' => Parametre::get('atelier.email', config('garage.email', 'contact@garage-moderne.local')),
            'nif' => Parametre::get('atelier.nif', config('garage.nif', '')),
        ];

        $html = View::make('pdf.ordre', compact('ordre', 'type', 'titre', 'numero', 'atelier'))->render();

        if (class_exists(Pdf::class)) {
            return Pdf::loadHTML($html)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'DejaVu Sans',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => false,
                ]);
        }

        return response($html)->header('Content-Type', 'text/html');
    }

    public function telecharger(OrdreReparation $ordre, string $type = 'devis'): mixed
    {
        $pdf = $this->genererOrdre($ordre, $type);
        $filename = strtolower($type).'-'.$ordre->numero.'.pdf';

        if (is_object($pdf) && method_exists($pdf, 'download')) {
            return $pdf->download($filename);
        }

        return $pdf;
    }

    public function stream(OrdreReparation $ordre, string $type = 'devis'): mixed
    {
        $pdf = $this->genererOrdre($ordre, $type);
        $filename = strtolower($type).'-'.$ordre->numero.'.pdf';

        if (is_object($pdf) && method_exists($pdf, 'stream')) {
            return $pdf->stream($filename);
        }

        return $pdf;
    }
}
