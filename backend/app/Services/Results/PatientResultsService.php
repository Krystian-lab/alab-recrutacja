<?php

namespace App\Services\Results;

use App\Models\Patient;

class PatientResultsService
{
    /**
     * Buduje strukturę odpowiedzi dla danego pacjenta:
     */
    public function buildResponse(Patient $patient): array
    {

        $patient->loadMissing('orders.results');

        return [
            'patient' => [
                'id'        => $patient->id,
                'name'      => $patient->name,
                'surname'   => $patient->surname,
                'sex'       => $patient->sex_short, 
                'birthDate' => optional($patient->birth_date)->format('Y-m-d'),
            ],
            'orders' => $patient->orders->map(function ($order) {
                return [
                    'orderId' => $order->order_id_ext,
                    'results' => $order->results->map(function ($result) {
                        return [
                            'name'      => $result->name,
                            'value'     => $result->value,
                            'reference' => $result->reference,
                        ];
                    })->values()->all(),
                ];
            })->values()->all(),
        ];
    }
}
