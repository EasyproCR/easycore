<?php

namespace App\Providers\Filament;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Shanerbaner82\PanelRoles\PanelRoles;

class PersonalPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->favicon(config('app.env') === 'production' ? secure_asset('images/favicon.ico') : asset('images/favicon.ico'))
            ->id('personal')
            ->path('personal')
            ->login()
            ->registration()
            ->maxContentWidth(MaxWidth::Full)
            ->default()
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Purple,
                'warning' => Color::Yellow,
            ])
            ->discoverResources(in: app_path('Filament/Personal/Resources'), for: 'App\\Filament\\Personal\\Resources')
            ->discoverPages(in: app_path('Filament/Personal/Pages'), for: 'App\\Filament\\Personal\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->navigationItems([
                NavigationItem::make('EVA')
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->url('/personal/easychat', true)
                    ->sort(999),
            ])
            ->discoverWidgets(in: app_path('Filament/Personal/Widgets'), for: 'App\\Filament\\Personal\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            // ->databaseNotifications()
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                PanelRoles::make()
                ->roleToAssign('panel_user')
                ->restrictedRoles(['panel_user', 'super_admin', 'soporte', 'ventas', 'servicio_al_cliente', 'rrhh', 'contabilidad', 'gerente']),
                FilamentApexChartsPlugin::make(),
                FilamentEditProfilePlugin::make()
                    ->setIcon('heroicon-o-user')
                    ->shouldShowAvatarForm()
                    ->setNavigationGroup(__('resources.app.navigation_group'))
                    ->shouldShowDeleteAccountForm(true)
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->url(fn(): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-user-circle'),
                MenuItem::make()
                    ->label('Panel de Administración')
                    ->url('/admin')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->visible(function (){
                        if(auth()->user()){
                            if(auth()->user()?->hasAnyRole([
                                'super_admin'
                            ])){
                                return true;
                            }else{
                                return false;
                            }
                        }else{
                            return false;
                        }
                        ;
                    }),

                MenuItem::make()
                    ->label('Panel de Soporte')
                    ->url('/soporte')
                    ->icon('heroicon-o-check-badge')
                    ->visible(fn (): bool => auth()->user()?->hasAnyRole([
                        'soporte',
                    ])),

                MenuItem::make()
                    ->label('Panel de RRHH')
                    ->url('/rrhh')
                    ->icon('heroicon-o-cursor-arrow-ripple')
                    ->visible(fn (): bool => auth()->user()?->hasAnyRole([
                        'rrhh',
                    ])),

                MenuItem::make()
                    ->label('Panel de Ventas')
                    ->url('/sales')
                    ->icon('heroicon-o-currency-dollar')
                    ->visible(fn (): bool => auth()->user()?->hasAnyRole([
                        'ventas',
                    ])),

                MenuItem::make()
                    ->label('Panel de Gerencia')
                    ->url('/ops')
                    ->icon('heroicon-o-server')
                    ->visible(fn (): bool => auth()->user()?->hasAnyRole([
                        'gerente',
                    ])),

                MenuItem::make()
                    ->label('Panel de Servicio al Cliente')
                    ->url('/services')
                    ->icon('heroicon-o-check-badge')
                    ->visible(fn (): bool => auth()->user()?->hasAnyRole([
                        'servicio_al_cliente',
                    ])),


                MenuItem::make()
                    ->label('Panel de Contabilidad')
                    ->url('/contabilidad')
                    ->icon('heroicon-o-document-currency-dollar')
                    ->visible(fn (): bool => auth()->user()?->hasAnyRole([
                        'contabilidad',
                    ])),
            ])
            ;

    }

    public function boot()
    {

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['es','en']); // also accepts a closure
        });

    }
}
