import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Currency } from '../models/currency.model';
import { environment } from '../../../environments/environment';

@Injectable({
    providedIn: 'root'
})
export class CurrencyService {
    private apiUrl = `${environment.apiUrl}/projects/currencies`;

    constructor(private http: HttpClient) { }

    getCurrencies(): Observable<Currency[]> {
        return this.http.get<Currency[]>(this.apiUrl);
    }
}
