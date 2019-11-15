import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AsideFeaturedPostsComponent } from './aside-featured-posts.component';

describe('AsideFeaturedPostsComponent', () => {
  let component: AsideFeaturedPostsComponent;
  let fixture: ComponentFixture<AsideFeaturedPostsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AsideFeaturedPostsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AsideFeaturedPostsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
