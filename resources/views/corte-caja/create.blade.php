@extends('layout.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Abrir Nuevo Corte de Caja</h5>
            </div>
            <div class="card-body">
                @if($corteAbierto)
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Corte Abierto Existente</h6>
                    <p>Ya tienes un corte de caja abierto. No puedes abrir otro hasta cerrar el actual.</p>
                    <div class="mt-3">
                        <a href="{{ route('corte-caja.show', $corteAbierto->id) }}" class="btn btn-primary">
                            <i class="fas fa-eye me-2"></i>Ver Corte Actual
                        </a>
                        <a href="{{ route('corte-caja.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list me-2"></i>Ver Todos los Cortes
                        </a>
                    </div>
                </div>
                @else
                <form id="corte-form">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="monto_inicial" class="form-label">Monto Inicial en Caja *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" class="form-control" 
                                   id="monto_inicial" name="monto_inicial" required 
                                   placeholder="0.00">
                        </div>
                        <div class="form-text">Ingrese el monto con el que inicia la caja</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notas" class="form-label">Notas (Opcional)</label>
                        <textarea class="form-control" id="notas" name="notas" 
                                  rows="3" placeholder="Observaciones del corte..."></textarea>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Importante</h6>
                        <ul class="mb-0">
                            <li>Verifique el monto inicial antes de confirmar</li>
                            <li>Este corte permanecerá abierto hasta que sea cerrado</li>
                            <li>Todas las ventas se registrarán en este corte</li>
                        </ul>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-lock-open me-2"></i>Abrir Corte de Caja
                        </button>
                        <a href="{{ route('corte-caja.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
                @endif
            </div>
        </div>
        
        <!-- Información del último corte -->
        @if($ultimoCorte)
        <div class="card shadow mt-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">Último Corte Cerrado</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Fecha:</strong> {{ $ultimoCorte->fecha }}</p>
                        <p><strong>Monto Inicial:</strong> ${{ number_format($ultimoCorte->monto_inicial, 2) }}</p>
                        <p><strong>Monto Final:</strong> ${{ number_format($ultimoCorte->monto_final, 2) }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Ventas Efectivo:</strong> ${{ number_format($ultimoCorte->ventas_efectivo, 2) }}</p>
                        <p><strong>Diferencia:</strong> 
                            <span class="badge bg-{{ $ultimoCorte->diferencia == 0 ? 'success' : 'danger' }}">
                                ${{ number_format($ultimoCorte->diferencia, 2) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    @if(!$corteAbierto)
    // Formulario de apertura de corte
    $('#corte-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            monto_inicial: $('#monto_inicial').val(),
            notas: $('#notas').val()
        };
        
        if (parseFloat(formData.monto_inicial) < 0) {
            Swal.fire('Error', 'El monto inicial no puede ser negativo', 'error');
            return;
        }
        
        Swal.fire({
            title: '¿Confirmar apertura?',
            text: 'Se abrirá un nuevo corte de caja con el monto especificado',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, abrir corte',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('corte-caja.store') }}",
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        Swal.showLoading();
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Corte Abierto!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = "{{ route('corte-caja.show', '') }}/" + response.corte_id;
                        });
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON.message, 'error');
                    }
                });
            }
        });
    });
    
    // Formato automático del monto
    $('#monto_inicial').on('blur', function() {
        let value = parseFloat($(this).val());
        if (!isNaN(value)) {
            $(this).val(value.toFixed(2));
        }
    });
    @endif
});
</script>

<style>
.card {
    border: none;
    border-radius: 15px;
}
.btn-lg {
    padding: 12px 24px;
    font-size: 1.1rem;
}
.form-control {
    border-radius: 8px;
}
.alert {
    border-radius: 10px;
}
</style>
@endsection