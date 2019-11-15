import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AsideCategoriesComponent } from './aside-categories.component';

describe('AsideCategoriesComponent', () => {
  let component: AsideCategoriesComponent;
  let fixture: ComponentFixture<AsideCategoriesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AsideCategoriesComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AsideCategoriesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
