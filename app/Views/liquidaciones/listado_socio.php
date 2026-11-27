<?php #echo $titulo; 
?>

<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title">Información del socio</h5>
        <div class="row">
            <div class="col-md-6">
                <strong>Nombre:</strong>
                <?= esc(trim(($socio['nombre'] ?? '') . ' ' . ($socio['apellido'] ?? ''))); ?>
            </div>
            <div class="col-md-6">
                <strong>D.N.I.:</strong> <?= esc($socio['dni'] ?? '-'); ?>
            </div>
            <div class="col-md-6">
                <strong>Teléfono:</strong> <?= esc($socio['telefono'] ?? '-'); ?>
            </div>
            <div class="col-md-6">
                <strong>Email:</strong> <?= esc($socio['email'] ?? '-'); ?>
            </div>
            <div class="col-12">
                <strong>Domicilio:</strong> <?= esc($socio['domicilio'] ?? '-'); ?>
            </div>
        </div>
    </div>
</div>

<br><br>
<!--<a class="btn btn-primary" href="<?= base_url(); ?>public/liquidaciones/nuevo">+ Nueva liquidación</a>
<a class="btn btn-secondary" href="<?= base_url(); ?>public/socios">Volver a socios</a>-->
<br><br>
<?php if (session()->getFlashdata('error')) { ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div>
<?php } ?>

<table class="table table-hover" id="contenido-lista">
    <thead>
        <tr>
            <th>#</th>
            <th>Liquidación</th>
            <th>Monto</th>
            <th>Fecha de Vencimiento</th>
            <th>Estado</th>
            <th>Último cobrador</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($historial)) {
            foreach ($historial as $h) {
                echo '<tr>';
                echo '<td>' . $h['liquidacion']['id'] . '</td>';
                echo '<td>' . $h['liquidacion']['nombre'] . '</td>';
                echo '<td>$' . ($h['total_pagado'] ?? 0) . ' / $' . $h['liquidacion']['monto'] . '</td>';
                echo '<td>' . date('d/m/Y', strtotime($h['liquidacion']['fecha_vencimiento'])) . '</td>';
                echo '<td>' . $h['estado'] . '</td>';
                $cobrador = $h['pago'] && !empty($h['pago']['id_recaudador'])
                    ? trim(($h['pago']['recaudador_apellido'] ?? '') . ', ' . ($h['pago']['recaudador_nombre'] ?? ''))
                    : 'No registrado';
                echo '<td>' . esc($cobrador) . '</td>';
                echo '<td>';
                if ($h['estado'] !== 'Pagado' && $puedePagar) {
                    $urlBasePago = base_url('public/pagos/nuevo/' . $idSocio . '/' . $h['liquidacion']['id']);
                    echo '<div class="d-flex flex-column gap-1">';

                    if ($h['estado'] === 'Pago vencido') {
                        echo '<div class="input-group input-group-sm" style="max-width: 140px;">';
                        echo '<input type="number" class="form-control input-interes-vencido" '
                            . 'data-base-url="' . esc($urlBasePago, 'attr') . '" '
                            . 'min="0" step="0.01" placeholder="% interés" aria-label="Porcentaje de interés">';
                        echo '<span class="input-group-text">%</span>';
                        echo '</div>';
                    }

                    echo '<a class="btn btn-success btn-sm btn-pagar-liq" href="' . esc($urlBasePago, 'attr') . '">Pagar</a>';
                    echo '</div>';
                } elseif ($h['estado'] === 'Pagado') {
                    echo '<button class="btn btn-secondary btn-sm" disabled>Pago realizado</button>';
                } else {
                    echo '<span class="text-muted">Sin permiso de pago</span>';
                }
                echo '</td>';
                echo '</tr>';
            }
        }
        ?>
    </tbody>
</table>

<script>
document.querySelectorAll('.input-interes-vencido').forEach((input) => {
    const actualizarHref = () => {
        const fila = input.closest('td');
        const enlace = fila ? fila.querySelector('.btn-pagar-liq') : null;
        if (!enlace) return;

        const baseUrl = input.dataset.baseUrl;
        const rawVal = input.value.trim();
        const porcentaje = rawVal !== '' ? Math.max(0, parseFloat(rawVal) || 0) : 0;

        enlace.href = porcentaje > 0
            ? `${baseUrl}?interes=${encodeURIComponent(porcentaje)}`
            : baseUrl;
    };

    input.addEventListener('input', actualizarHref);
    input.addEventListener('change', actualizarHref);
});
</script>