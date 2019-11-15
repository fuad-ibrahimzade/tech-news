import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import {PageNotFoundComponent} from "./page-not-found/page-not-found.component";
import {MainComponent as HomeMainComponent} from "./home/main/main.component";
import {NewsComponent as HomeNewsComponent} from "./home/news/news.component";
import {PopularComponent as HomePopularComponent} from "./home/popular/popular.component";
import {AboutComponent as HomeAboutComponent} from "./home/about/about.component";
import {ContactsComponent as HomeContactsComponent} from "./home/contacts/contacts.component";
import {CategoryComponent} from "./home/category/category.component";
import {BlogPostComponent} from "./home/blog-post/blog-post.component";

import {DashboardComponent} from "./admin/dashboard/dashboard.component";
import {AuthGuardService} from "./admin/services/auth-guard.service";
import {LoginComponent} from "./admin/login/login.component";
import {CreatePageComponent} from "./admin/create-page/create-page.component";
import {ListPagesComponent} from "./admin/list-pages/list-pages.component";
import {UpdatePageComponent} from "./admin/update-page/update-page.component";
import {CreatePostComponent} from "./admin/create-post/create-post.component";
import {ListPostsComponent} from "./admin/list-posts/list-posts.component";
import {UpdatePostComponent} from "./admin/update-post/update-post.component";
import {UpdateAboutComponent} from "./admin/update-about/update-about.component";
import {UpdateContactsComponent} from "./admin/update-contacts/update-contacts.component";
import {UpdateSocialLinksComponent} from "./admin/update-social-links/update-social-links.component";
import { UpdateAdvertisementsComponent } from './admin/update-advertisements/update-advertisements.component';


const routes: Routes = [
    { path: '', redirectTo: 'home', pathMatch: 'full' },
    { path: 'home', component: HomeMainComponent, canActivate: [AuthGuardService] },
    { path: 'news', component: HomeNewsComponent, canActivate: [AuthGuardService]},
    { path: 'popular', component: HomePopularComponent, canActivate: [AuthGuardService] },
    { path: 'web-design', component: CategoryComponent, canActivate: [AuthGuardService] },
    { path: 'javascript', component: CategoryComponent, canActivate: [AuthGuardService] },
    { path: 'css', component: CategoryComponent, canActivate: [AuthGuardService] },
    { path: 'jquery', component: CategoryComponent, canActivate: [AuthGuardService] },
    { path: 'category/:category', component: CategoryComponent, canActivate: [AuthGuardService] },
    { path: 'about', component: HomeAboutComponent, canActivate: [AuthGuardService] },
    { path: 'contacts', component: HomeContactsComponent, canActivate: [AuthGuardService] },
    { path: 'blog-post/:id', component: BlogPostComponent, canActivate: [AuthGuardService] },


    { path: 'admin', redirectTo:'dashboard/login',pathMatch: 'full' },
    { path: 'dashboard/login', component: LoginComponent, canActivate: [AuthGuardService] },
    { path: 'dashboard/logout', component: DashboardComponent, canActivate: [AuthGuardService] },
    { path: 'dashboard', component: DashboardComponent, canActivate: [AuthGuardService] },
    { path: 'dashboard/create-page', component: CreatePageComponent, canActivate: [AuthGuardService] },
    { path: 'dashboard/list-pages', component: ListPagesComponent, canActivate: [AuthGuardService] },
    { path: 'dashboard/update-page/:id', component: UpdatePageComponent, canActivate: [AuthGuardService] },
    { path: 'dashboard/create-post', component: CreatePostComponent, canActivate: [AuthGuardService] },
    { path: 'dashboard/list-posts', component: ListPostsComponent, canActivate: [AuthGuardService] },
    { path: 'dashboard/update-post/:id', component: UpdatePostComponent, canActivate: [AuthGuardService] },

    { path: 'dashboard/update-about', component: UpdateAboutComponent, canActivate: [AuthGuardService] },
    { path: 'dashboard/update-contacts', component: UpdateContactsComponent, canActivate: [AuthGuardService] },
    { path: 'dashboard/update-social-links', component: UpdateSocialLinksComponent, canActivate: [AuthGuardService] },
    { path: 'dashboard/update-advertisements', component: UpdateAdvertisementsComponent, canActivate: [AuthGuardService] },

    { path: '**', component: PageNotFoundComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes,{onSameUrlNavigation: 'reload'})],
  exports: [RouterModule]
})
export class AppRoutingModule { }
