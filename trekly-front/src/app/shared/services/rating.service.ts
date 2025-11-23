import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { AuthService } from '../../auth/auth.service';

@Injectable({
    providedIn: 'root'
})
export class RatingService {
    private apiUrl = '/api/ratings';

    constructor(private http: HttpClient, private authService: AuthService) { }

    private getHeaders(): HttpHeaders {
        const user = this.authService.currentUserValue;
        console.log('getHeaders - user:', user);
        let headers = new HttpHeaders();
        console.log('getHeaders - initial headers:', headers);
        if (user && user.token) {
            console.log('getHeaders - Adding Authorization header with token');
            headers = headers.set('Authorization', `Bearer ${user.token}`);
            console.log('getHeaders - headers after set:', headers);
            console.log('getHeaders - Authorization value:', headers.get('Authorization'));
        } else {
            console.log('getHeaders - NO TOKEN FOUND');
        }
        return headers;
    }

    rateCreator(creatorId: string, projectId: string, rating: number, comment?: string): Observable<any> {
        const token = this.authService.currentUserValue?.token;
        console.log('=== RatingService DEBUG ===');
        console.log('Token exists:', token ? 'YES' : 'NO');
        console.log('User object:', this.authService.currentUserValue);
        console.log('Headers being sent:', this.getHeaders());

        return this.http.post(`${this.apiUrl}`, {
            creatorId,
            projectId,
            rating,
            comment
        }, { headers: this.getHeaders() });
    }

    getCreatorRatings(creatorId: string): Observable<any[]> {
        return this.http.get<any[]>(`${this.apiUrl}/creator/${creatorId}`);
    }

    getMemberRatings(memberId: string): Observable<any[]> {
        return this.http.get<any[]>(`${this.apiUrl}/member/${memberId}`);
    }

    getAverageRating(creatorId: string): Observable<any> {
        return this.http.get<any>(`${this.apiUrl}/creator/${creatorId}/average`);
    }
}
