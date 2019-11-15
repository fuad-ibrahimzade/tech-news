import { TestBed } from '@angular/core/testing';

import { HomeScriptsService } from './home-scripts.service';

describe('HomeScriptsService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: HomeScriptsService = TestBed.get(HomeScriptsService);
    expect(service).toBeTruthy();
  });
});
