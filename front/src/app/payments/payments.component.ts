import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { PaymentsService } from '../shared/services/payments.service';
import { AuthService } from '../auth/auth.service';

@Component({
  selector: 'app-payments',
  templateUrl: './payments.component.html',
  styleUrls: ['./payments.component.scss']
  , standalone: true
  , imports: [CommonModule, FormsModule]
})
export class PaymentsComponent implements OnInit {
  cuentas: any[] = [];
  cuentasVencidas: any[] = [];
  puedeCrearAventura = true;
  isCreator = false;
  cuentaSeleccionada: any = null;
  pago: any = {};

  constructor(
    private authService: AuthService,
    private paymentsService: PaymentsService
  ) {}

  ngOnInit() {
    const user = this.authService.currentUserValue;
    this.isCreator = user?.role === 'CREATOR';
    if (this.isCreator) {
      const creatorId = user.userId;
      this.paymentsService.getCuentasPorCreator(creatorId).subscribe(cuentas => {
        this.cuentas = cuentas;
        this.cuentasVencidas = cuentas.filter(c => c.estado === 'VENCIDA');
        this.puedeCrearAventura = this.cuentasVencidas.length === 0;
      });
    }
  }

  seleccionarCuenta(cuenta: any) {
    this.cuentaSeleccionada = cuenta;
    this.pago = {
      cuenta_por_pagar_id: cuenta.id,
      creator_id: this.authService.currentUserValue.userId,
      monto: cuenta.monto,
      metodo_pago: '',
      referencia_pago: ''
    };
  }

  enviarPago() {
    this.paymentsService.registrarPago(this.pago).subscribe(resp => {
      alert('Pago registrado correctamente');
      this.cuentaSeleccionada = null;
      this.ngOnInit();
    });
  }
}
