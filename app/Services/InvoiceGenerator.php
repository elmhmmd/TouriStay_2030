<?php

namespace App\Services;

use App\Models\Booking;
use FPDF;

class InvoiceGenerator
{
    protected $booking;

    // Extend FPDF to add a custom footer
    protected $pdf;

    public function generate(Booking $booking)
    {
        $this->booking = $booking->load('annonce', 'annonce.user', 'annonce.typeDeLogement', 'annonce.equipements');

        // Create a custom FPDF class with a footer
        $this->pdf = new class extends FPDF {
            function Footer()
            {
                // Position at 30 mm from bottom
                $this->SetY(-30);
                $this->SetFont('Arial', 'I', 8);
                $this->SetTextColor(16, 185, 129);
                $this->Cell(0, 10, 'Thank you for choosing TouriStay!', 0, 1, 'C');
                $this->SetTextColor(0, 0, 0);
            }
        };

        $this->pdf->AddPage();
        $this->pdf->SetFont('Arial', 'B', 16);

        // Header
        $this->pdf->SetTextColor(16, 185, 129);
        $this->pdf->Cell(0, 10, 'TouriStay Invoice', 0, 1, 'C');
        $this->pdf->SetDrawColor(239, 68, 68);
        $this->pdf->Line(10, 20, 200, 20);
        $this->pdf->Ln(10);

        // Reset text color
        $this->pdf->SetTextColor(0, 0, 0);

        // Invoice Details
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->Cell(0, 10, 'Invoice Details', 0, 1);
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(0, 8, 'Invoice Date: ' . now()->format('Y-m-d'), 0, 1);
        $this->pdf->Cell(0, 8, 'Booking ID: ' . $booking->id, 0, 1);
        $this->pdf->Ln(5);

        // Booking Summary
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->Cell(0, 10, 'Booking Summary', 0, 1);
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(0, 8, 'Listing: ' . $booking->annonce->location, 0, 1);
        $this->pdf->Cell(0, 8, 'Type: ' . $booking->annonce->typeDeLogement->name, 0, 1);
        $this->pdf->Cell(0, 8, 'Dates: ' . $booking->start_date->format('Y-m-d') . ' to ' . $booking->end_date->format('Y-m-d'), 0, 1);
        $this->pdf->Cell(0, 8, 'Price per Night: $' . $booking->annonce->price, 0, 1);
        $this->pdf->Cell(0, 8, 'Total Price: $' . $booking->total_price, 0, 1);
        $this->pdf->Cell(0, 8, 'Equipment: ' . $booking->annonce->equipements->pluck('name')->join(', '), 0, 1);
        $this->pdf->Ln(5);

        // Proprietaire Details
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->Cell(0, 10, 'Proprietaire Details', 0, 1);
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell(0, 8, 'Name: ' . $booking->annonce->user->name, 0, 1);
        $this->pdf->Cell(0, 8, 'Email: ' . $booking->annonce->user->email, 0, 1);
        $this->pdf->Ln(5);

        // Output the PDF
        return $this->pdf->Output('D', 'invoice_' . $booking->id . '.pdf');
    }
}