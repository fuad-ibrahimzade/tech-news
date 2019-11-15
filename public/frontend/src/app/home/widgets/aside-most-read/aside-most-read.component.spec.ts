import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AsideMostReadComponent } from './aside-most-read.component';

describe('AsideMostReadComponent', () => {
  let component: AsideMostReadComponent;
  let fixture: ComponentFixture<AsideMostReadComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AsideMostReadComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AsideMostReadComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
