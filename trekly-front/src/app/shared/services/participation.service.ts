import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { AuthService } from '../../auth/auth.service';

@Injectable({
    providedIn: 'root'
})
export class ParticipationService {
    private apiUrl = '/api/participations';

    constructor(private http: HttpClient, private authService: AuthService) { }

    private getHeaders(): HttpHeaders {
        const user = this.authService.currentUserValue;
        let headers = new HttpHeaders();
        if (user && user.token) {
            headers = headers.set('Authorization', `Bearer ${user.token}`);
        }
        return headers;
    }

    joinProject(projectId: string): Observable<any> {
        return this.http.post(`${this.apiUrl}`, {
            projectId
        }, { headers: this.getHeaders() });
    }

    getUserParticipations(userId: string): Observable<any[]> {
        return this.http.get<any[]>(`${this.apiUrl}/user/${userId}`, { headers: this.getHeaders() });
    }
}
