import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { UpdateAboutComponent } from './update-about.component';

describe('UpdateAboutComponent', () => {
  let component: UpdateAboutComponent;
  let fixture: ComponentFixture<UpdateAboutComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ UpdateAboutComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(UpdateAboutComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
