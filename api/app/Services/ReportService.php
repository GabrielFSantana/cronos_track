<?php

namespace App\Services;

class ReportService
{
    /**
     * Agregações usadas tanto por GET /api/report quanto pelo dashboard:
     * total do período, e quebras por app/domínio, por projeto e por dia.
     * Duração sempre via SUM(duracao_seg), nunca recalculada com TIMESTAMPDIFF.
     */
    public function aggregate(?string $de, ?string $ate, ?int $projetoId): array
    {
        $db = db_connect();

        $applyFilters = static function ($builder) use ($de, $ate, $projetoId) {
            if ($de !== null) {
                $builder->where('DATE(inicio) >=', $de);
            }
            if ($ate !== null) {
                $builder->where('DATE(inicio) <=', $ate);
            }
            if ($projetoId !== null) {
                $builder->where('projeto_id', $projetoId);
            }

            return $builder;
        };

        $totalRow = $applyFilters($db->table('activities'))
            ->selectSum('duracao_seg')
            ->get()
            ->getRowArray();

        $porApp = $applyFilters($db->table('activities'))
            ->select('app_ou_dominio')
            ->selectSum('duracao_seg')
            ->groupBy('app_ou_dominio')
            ->orderBy('duracao_seg', 'DESC')
            ->get()
            ->getResultArray();

        $porProjeto = $applyFilters($db->table('activities a'))
            ->select('a.projeto_id, p.nome, p.cor')
            ->selectSum('a.duracao_seg', 'duracao_seg')
            ->join('projetos p', 'p.id = a.projeto_id', 'left')
            ->groupBy('a.projeto_id')
            ->orderBy('duracao_seg', 'DESC')
            ->get()
            ->getResultArray();

        $porDia = $applyFilters($db->table('activities'))
            ->select('DATE(inicio) AS dia')
            ->selectSum('duracao_seg')
            ->groupBy('dia')
            ->orderBy('dia', 'ASC')
            ->get()
            ->getResultArray();

        return [
            'total_seg'   => (int) ($totalRow['duracao_seg'] ?? 0),
            'por_app'     => $this->castDuracao($porApp),
            'por_projeto' => $this->castDuracao($porProjeto),
            'por_dia'     => $this->castDuracao($porDia),
        ];
    }

    private function castDuracao(array $rows): array
    {
        foreach ($rows as &$row) {
            $row['duracao_seg'] = (int) $row['duracao_seg'];

            if (array_key_exists('projeto_id', $row) && $row['projeto_id'] !== null) {
                $row['projeto_id'] = (int) $row['projeto_id'];
            }
        }

        return $rows;
    }
}
