import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AsideAddComponent } from './aside-add.component';

describe('AsideAddComponent', () => {
  let component: AsideAddComponent;
  let fixture: ComponentFixture<AsideAddComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AsideAddComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AsideAddComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
