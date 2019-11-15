import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { UpdateAdvertisementsComponent } from './update-advertisements.component';

describe('UpdateAdvertisementsComponent', () => {
  let component: UpdateAdvertisementsComponent;
  let fixture: ComponentFixture<UpdateAdvertisementsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ UpdateAdvertisementsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(UpdateAdvertisementsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
