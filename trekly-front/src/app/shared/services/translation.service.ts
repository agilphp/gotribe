import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';

@Injectable({
    providedIn: 'root'
})
export class TranslationService {
    private currentLang = new BehaviorSubject<string>('es');
    private translations: any = {};

    currentLang$: Observable<string> = this.currentLang.asObservable();

    constructor(private http: HttpClient) {
        // Load saved language preference or default to Spanish
        const savedLang = localStorage.getItem('preferredLanguage') || 'es';
        this.setLanguage(savedLang);
    }

    setLanguage(lang: string): void {
        this.http.get(`/assets/i18n/${lang}.json`).subscribe(
            (translations) => {
                this.translations = translations;
                this.currentLang.next(lang);
                localStorage.setItem('preferredLanguage', lang);
            },
            (error) => {
                console.error(`Failed to load translations for ${lang}`, error);
            }
        );
    }

    getCurrentLanguage(): string {
        return this.currentLang.value;
    }

    translate(key: string): string {
        const keys = key.split('.');
        let value = this.translations;

        for (const k of keys) {
            if (value && value[k]) {
                value = value[k];
            } else {
                return key; // Return key if translation not found
            }
        }

        return value;
    }

    instant(key: string): string {
        return this.translate(key);
    }
}
