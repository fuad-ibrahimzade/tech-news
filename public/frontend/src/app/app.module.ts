import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import {enableProdMode} from '@angular/core';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { IndexComponent } from './home/index/index.component';
import {PageNotFoundComponent} from "./page-not-found/page-not-found.component";
import { NewsComponent } from './home/news/news.component';
import { PopularComponent } from './home/popular/popular.component';
import { AboutComponent } from './home/about/about.component';
import { ContactsComponent } from './home/contacts/contacts.component';
import { HeadComponent as HomeHeadComponent } from './home/parts/head/head.component';
import { NavComponent as HomeNavComponent} from './home/parts/nav/nav.component';
import { FooterComponent as HomeFooterComponent} from './home/parts/footer/footer.component';
import { BlogPostComponent } from './home/blog-post/blog-post.component';
import { CategoryComponent } from './home/category/category.component';
import { DashboardComponent } from './admin/dashboard/dashboard.component';
import { LoginComponent } from './admin/login/login.component';
import { JwtModule } from '@auth0/angular-jwt';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import {FormsModule} from "@angular/forms";
import { MainComponent } from './home/main/main.component';
import { CreatePageComponent } from './admin/create-page/create-page.component';
import { ListPagesComponent } from './admin/list-pages/list-pages.component';
import { UpdatePageComponent } from './admin/update-page/update-page.component';
import { CreatePostComponent } from './admin/create-post/create-post.component';
import { UpdatePostComponent } from './admin/update-post/update-post.component';
import { ListPostsComponent } from './admin/list-posts/list-posts.component';
import { UpdateAboutComponent } from './admin/update-about/update-about.component';
import { UpdateContactsComponent } from './admin/update-contacts/update-contacts.component';
import { UpdateSocialLinksComponent } from './admin/update-social-links/update-social-links.component';
import { RichtextComponent } from './admin/richtext/richtext.component';
import { AuthTokenInterceptor } from './admin/services/auth-token-interceptor';
import { config } from './config';
import { AsideMostReadComponent } from './home/widgets/aside-most-read/aside-most-read.component';
import { AsideFeaturedPostsComponent } from './home/widgets/aside-featured-posts/aside-featured-posts.component';
import { AsideCategoriesComponent } from './home/widgets/aside-categories/aside-categories.component';
import { AsideTagsComponent } from './home/widgets/aside-tags/aside-tags.component';
import { AsideArchiveComponent } from './home/widgets/aside-archive/aside-archive.component';
import { AsideAddComponent } from './home/widgets/aside-add/aside-add.component';
import {NgxPaginationModule} from 'ngx-pagination';
import { ShareModule } from '@ngx-share/core';
import { UpdateAdvertisementsComponent } from './admin/update-advertisements/update-advertisements.component';
import { CommentsComponent } from './home/widgets/comments/comments.component';

export function tokenGetter() {
    return sessionStorage.getItem("access_token");
}
enableProdMode();

@NgModule({
  declarations: [
    AppComponent,
    PageNotFoundComponent,
    IndexComponent,
    NewsComponent,
    PopularComponent,
    AboutComponent,
    ContactsComponent,
    HomeHeadComponent,
    HomeNavComponent,
    HomeFooterComponent,
    BlogPostComponent,
    CategoryComponent,
    DashboardComponent,
    LoginComponent,
    MainComponent,
    CreatePageComponent,
    ListPagesComponent,
    UpdatePageComponent,
    CreatePostComponent,
    UpdatePostComponent,
    ListPostsComponent,
    UpdateAboutComponent,
    UpdateContactsComponent,
    UpdateSocialLinksComponent,
    RichtextComponent,
    AsideMostReadComponent,
    AsideFeaturedPostsComponent,
    AsideCategoriesComponent,
    AsideTagsComponent,
    AsideArchiveComponent,
    AsideAddComponent,
    UpdateAdvertisementsComponent,
    CommentsComponent,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    JwtModule.forRoot({
      config: {
          tokenGetter: tokenGetter
      }
    }),
    FormsModule,
    NgxPaginationModule,
    ShareModule
  ],
  providers: [
    { provide: HTTP_INTERCEPTORS, useClass: AuthTokenInterceptor, multi: true
    }
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
