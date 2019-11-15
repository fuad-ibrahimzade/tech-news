import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AsideTagsComponent } from './aside-tags.component';

describe('AsideTagsComponent', () => {
  let component: AsideTagsComponent;
  let fixture: ComponentFixture<AsideTagsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AsideTagsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AsideTagsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
