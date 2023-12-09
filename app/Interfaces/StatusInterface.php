<?php

namespace App\Interfaces;

interface StatusInterface
{
    // Status Konsultasi
    CONST STATUS_REQUEST = 1;
    CONST STATUS_BOOKED = 2;
    CONST STATUS_CANCEL = 3;
    CONST STATUS_DITOLAK = 4;
    CONST STATUS_KONSULTASI = 5;
    CONST STATUS_MENUNGGU_RESEP = 6;
    CONST STATUS_SELESAI = 7;

    // Status Konsultasi Text
    CONST STATUS_REQUEST_TEXT = 'Proses Request';
    CONST STATUS_BOOKED_TEXT = 'Booked';
    CONST STATUS_CANCEL_TEXT = 'Request Dibatalkan';
    CONST STATUS_DITOLAK_TEXT = 'Request Ditolak';
    CONST STATUS_KONSULTASI_TEXT = 'Konsultasi Aktif';
    CONST STATUS_MENUNGGU_RESEP_TEXT = 'Menunggu Resep';
    CONST STATUS_SELESAI_TEXT = 'Konsultasi Selesai';
}