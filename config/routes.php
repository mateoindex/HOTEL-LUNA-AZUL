<?php

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\GuestController;
use App\Controllers\RoomController;
use App\Controllers\ReservationController;
use App\Controllers\UserController;
use App\Controllers\AuditController;
use App\Controllers\ReportController;

// auth
Router::get ('/',           [AuthController::class, 'showLogin']);
Router::get ('/login',      [AuthController::class, 'showLogin']);
Router::post('/login',      [AuthController::class, 'login']);
Router::post('/logout',     [AuthController::class, 'logout']);

// dashboard
Router::get ('/dashboard',  [DashboardController::class, 'index']);

// huespedes
Router::get ('/guests',                 [GuestController::class, 'index']);
Router::get ('/guests/create',          [GuestController::class, 'create']);
Router::post('/guests',                 [GuestController::class, 'store']);
Router::get ('/guests/{id}',            [GuestController::class, 'show']);
Router::get ('/guests/{id}/edit',       [GuestController::class, 'edit']);
Router::post('/guests/{id}',            [GuestController::class, 'update']);
Router::post('/guests/{id}/delete',     [GuestController::class, 'destroy']);
Router::get ('/api/guests/search',      [GuestController::class, 'apiSearch']);

// habitaciones
Router::get ('/rooms',                  [RoomController::class, 'index']);
Router::get ('/rooms/create',           [RoomController::class, 'create']);
Router::post('/rooms',                  [RoomController::class, 'store']);
Router::get ('/rooms/{id}/edit',        [RoomController::class, 'edit']);
Router::post('/rooms/{id}',             [RoomController::class, 'update']);
Router::post('/rooms/{id}/delete',      [RoomController::class, 'destroy']);

// reservas
Router::get ('/reservations',                   [ReservationController::class, 'index']);
Router::get ('/reservations/create',            [ReservationController::class, 'create']);
Router::post('/reservations',                   [ReservationController::class, 'store']);
Router::get ('/reservations/{id}',              [ReservationController::class, 'show']);
Router::get ('/reservations/{id}/edit',         [ReservationController::class, 'edit']);
Router::post('/reservations/{id}',              [ReservationController::class, 'update']);
Router::post('/reservations/{id}/cancel',       [ReservationController::class, 'cancel']);
Router::post('/reservations/{id}/check-in',     [ReservationController::class, 'checkIn']);
Router::post('/reservations/{id}/check-out',    [ReservationController::class, 'checkOut']);
Router::get ('/reservations/{id}/pdf',          [ReportController::class, 'reservationVoucher']);
Router::get ('/api/availability',               [ReservationController::class, 'apiAvailability']);

// usuarios
Router::get ('/users',                  [UserController::class, 'index']);
Router::get ('/users/create',           [UserController::class, 'create']);
Router::post('/users',                  [UserController::class, 'store']);
Router::get ('/users/{id}/edit',        [UserController::class, 'edit']);
Router::post('/users/{id}',             [UserController::class, 'update']);
Router::post('/users/{id}/deactivate',  [UserController::class, 'deactivate']);

// auditoria
Router::get ('/audit',          [AuditController::class, 'index']);
Router::get ('/api/audit',      [AuditController::class, 'apiList']);

// reportes
Router::get ('/reports',                [ReportController::class, 'index']);
Router::get ('/reports/occupancy.pdf',  [ReportController::class, 'occupancy']);
Router::get ('/reports/guests.pdf',     [ReportController::class, 'guestsList']);
