import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { UpdateSocialLinksComponent } from './update-social-links.component';

describe('UpdateSocialLinksComponent', () => {
  let component: UpdateSocialLinksComponent;
  let fixture: ComponentFixture<UpdateSocialLinksComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ UpdateSocialLinksComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(UpdateSocialLinksComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
