import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({
    providedIn: 'root'
})
export class UiService {
    private isSearchVisibleSubject = new BehaviorSubject<boolean>(false);
    isSearchVisible$ = this.isSearchVisibleSubject.asObservable();

    constructor() { }

    toggleSearch() {
        this.isSearchVisibleSubject.next(!this.isSearchVisibleSubject.value);
    }

    setSearchVisibility(isVisible: boolean) {
        this.isSearchVisibleSubject.next(isVisible);
    }
}
