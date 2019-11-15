import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AsideArchiveComponent } from './aside-archive.component';

describe('AsideArchiveComponent', () => {
  let component: AsideArchiveComponent;
  let fixture: ComponentFixture<AsideArchiveComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AsideArchiveComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AsideArchiveComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
