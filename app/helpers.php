<?php

if (!function_exists('formatDiasVida')) {
    /**
     * Formatea los días de vida en formato: X meses Y semanas Z días
     *
     * @param int $totalDias
     * @return string
     */
    function formatDiasVida($totalDias)
    {
        if ($totalDias < 0) {
            return '0 días';
        }

        $meses = intval($totalDias / 30);
        $diasRestantes = $totalDias % 30;
        $semanas = intval($diasRestantes / 7);
        $dias = $diasRestantes % 7;

        $partes = [];

        if ($meses > 0) {
            $partes[] = $meses . ' ' . ($meses == 1 ? 'mes' : 'meses');
        }

        if ($semanas > 0) {
            $partes[] = $semanas . ' ' . ($semanas == 1 ? 'semana' : 'semanas');
        }

        if ($dias > 0) {
            $partes[] = $dias . ' ' . ($dias == 1 ? 'día' : 'días');
        }

        // Si no hay partes (días = 0), mostrar "0 días"
        if (empty($partes)) {
            return '0 días';
        }

        // Unir las partes con comas y "y" para la última
        if (count($partes) == 1) {
            return $partes[0] . ' de vida';
        } elseif (count($partes) == 2) {
            return $partes[0] . ' y ' . $partes[1] . ' de vida';
        } else {
            $ultimaParte = array_pop($partes);
            return implode(', ', $partes) . ' y ' . $ultimaParte . ' de vida';
        }
    }
}