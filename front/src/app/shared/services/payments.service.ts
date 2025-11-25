import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class PaymentsService {
  private apiUrl = '/api/payments';

  constructor(private http: HttpClient) {}

  getCuentasPorCreator(creatorId: string): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/cuentas/creator/${creatorId}`);
  }

  registrarPago(pago: any): Observable<any> {
    return this.http.post(`${this.apiUrl}`, pago);
  }
}
