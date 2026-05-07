<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Middleware\AuthMiddleware;
use App\Models\Reservation;

class DashboardController extends Controller {

    public function index(): void {
        AuthMiddleware::require();

        $u = Auth::user();
        $arrivals   = Reservation::arrivalsToday();
        $departures = Reservation::departuresToday();
        $active     = Reservation::activeCount();
        $occupancy  = Reservation::occupancyToday();

        // ocupacion semana, empezando hoy
        $week = Reservation::weeklyOccupancy(today(), 7);

        $this->view('dashboard.index', [
            'pageTitle'  => 'Panel',
            'user'       => $u,
            'arrivals'   => $arrivals,
            'departures' => $departures,
            'active'     => $active,
            'occupancy'  => $occupancy,
            'week'       => $week,
        ]);
    }
}
