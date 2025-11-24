import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { AuthService } from '../auth/auth.service';
import { environment } from '../../environments/environment';

@Injectable({
    providedIn: 'root'
})
export class ProjectService {
    private apiUrl = `${environment.apiUrl}/projects/`;

    constructor(private http: HttpClient, private authService: AuthService) { }

    private getHeaders(): HttpHeaders {
        const user = this.authService.currentUserValue;
        let headers = new HttpHeaders();
        if (user && user.token) {
            // In a real app, we'd use an interceptor. For MVP, manual header.
            // Also, the backend expects X-User-Id for some ops if not decoding JWT fully in gateway.
            // But let's assume we send Authorization header and Gateway/Backend handles it.
            // Wait, our backend currently mocks X-User-Id or expects it from Gateway.
            // Let's send Authorization header.
            headers = headers.set('Authorization', `Bearer ${user.token}`);
        }
        return headers;
    }

    getProjects(): Observable<any[]> {
        return this.http.get<any[]>(this.apiUrl);
    }

    createProject(project: any): Observable<any> {
        return this.http.post<any>(this.apiUrl, project, { headers: this.getHeaders() });
    }
}
