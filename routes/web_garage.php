<?php

/**
 * Routes de l'application Garage Moderne.
 * À inclure dans routes/web.php :
 * require __DIR__.'/web_garage.php';
 */

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormulationPeintureController;
use App\Http\Controllers\OrdreReparationController;
use App\Http\Controllers\ParametreController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    // Tableau de bord
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Ordres de réparation
    Route::resource('ordres', OrdreReparationController::class)->except(['edit', 'update']);
    Route::patch('ordres/{ordre}/statut', [OrdreReparationController::class, 'updateStatut'])
        ->name('ordres.update-statut');
    Route::patch('ordres/{ordre}/facturation', [OrdreReparationController::class, 'updateFacturation'])
        ->name('ordres.update-facturation');
    Route::get('ordres/{ordre}/pdf/devis', [OrdreReparationController::class, 'pdfDevis'])
        ->name('ordres.pdf.devis');
    Route::get('ordres/{ordre}/pdf/facture', [OrdreReparationController::class, 'pdfFacture'])
        ->name('ordres.pdf.facture');
    Route::get('ordres/{ordre}/pdf/{type?}', [OrdreReparationController::class, 'pdfStream'])
        ->name('ordres.pdf.stream');

    // Clients
    Route::resource('clients', ClientController::class)->except(['destroy']);

    // Stock
    Route::get('stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('stock/create', [StockController::class, 'create'])->name('stock.create');
    Route::post('stock', [StockController::class, 'store'])->name('stock.store');
    Route::get('stock/alertes', [StockController::class, 'alertes'])->name('stock.alertes');
    Route::get('stock/laboratoire', [StockController::class, 'laboratoire'])->name('stock.laboratoire');
    Route::get('stock/formulation/create', [FormulationPeintureController::class, 'create'])->name('stock.formulation.create');
    Route::post('stock/formulation', [FormulationPeintureController::class, 'store'])->name('stock.formulation.store');
    Route::get('stock/mouvement', [StockController::class, 'mouvementForm'])->name('stock.mouvement');
    Route::post('stock/mouvement', [StockController::class, 'storeMouvement'])->name('stock.mouvement.store');
    Route::get('stock/{piece}', [StockController::class, 'show'])->name('stock.show');
    Route::get('stock/{piece}/edit', [StockController::class, 'edit'])->name('stock.edit');
    Route::put('stock/{piece}', [StockController::class, 'update'])->name('stock.update');

    // Paramètres atelier
    Route::get('parametres', [ParametreController::class, 'index'])->name('parametres.index');
    Route::put('parametres', [ParametreController::class, 'update'])->name('parametres.update');

    // Réception & inspection carrosserie 360°
    Route::get('reception/inspection', [ReceptionController::class, 'inspectionForm'])
        ->name('reception.inspection');
    Route::post('reception/inspection', [ReceptionController::class, 'storeInspection'])
        ->name('reception.inspection.store');

});
