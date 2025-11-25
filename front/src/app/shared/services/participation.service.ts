import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

export interface ValidationResult {
    valid: boolean;
    message: string;
    participantId?: string;
    projectId?: string;
    timestamp?: string;
    error?: string;
}

export interface Participation {
    id: string;
    projectId: string;
    userId: string;
    status: string;
    requestedAt: string;
}

@Injectable({
    providedIn: 'root'
})
export class ParticipationService {
    private apiUrl = `${environment.apiUrl}/participations`;

    constructor(private http: HttpClient) { }

    validateTicket(participationId: string): Observable<ValidationResult> {
        return this.http.post<ValidationResult>(`${this.apiUrl}/validate`, {
            participationId
        });
    }

    getUserParticipations(userId: string): Observable<Participation[]> {
        return this.http.get<Participation[]>(`${this.apiUrl}/user/${userId}`);
    }

    joinProject(projectId: string): Observable<any> {
        const currentUser = localStorage.getItem('currentUser');
        let token = '';
        if (currentUser) {
            try {
                token = JSON.parse(currentUser).token;
            } catch {}
        }
        let options = {};
        if (token) {
            options = {
                headers: new HttpHeaders({
                    Authorization: `Bearer ${token}`
                })
            };
        }
        return this.http.post(`${this.apiUrl}`, { projectId }, options);
    }
}
