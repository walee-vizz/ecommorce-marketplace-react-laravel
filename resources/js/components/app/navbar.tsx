import { Link, usePage } from "@inertiajs/react";
import { SidebarTrigger } from '@/components/ui/sidebar';
import { SharedData, type NavItem } from "@/types";
import CurrencyFormatter from '@/components/ui/currency-formatter';
import MiniCartDropdown from '@/components/app/mini-cart-dropdown';
// import AppLogo from './app-logo';
// import AppLogoIcon from './app-logo-icon';
// import { Icon } from '@/components/icon';
// import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
// import { LayoutGrid } from 'lucide-react';

// const mainNavItems: NavItem[] = [
//   {
//     title: 'Dashboard',
//     href: '/dashboard',
//     icon: LayoutGrid,
//   },
// ];

// const rightNavItems: NavItem[] = [
  // {
  //   title: 'Repository',
  //   href: 'https://github.com/laravel/react-starter-kit',
  //   icon: Folder,
  // },
  // {
  //   title: 'Documentation',
  //   href: 'https://laravel.com/docs/starter-kits',
  //   icon: BookOpen,
  // },
// ];

// const activeItemStyles = 'text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100';



export function Navbar({ useSidebar = true }) {
  const page = usePage<SharedData>();
  const { auth } = page.props;
  const user = auth.user;
  const isAllowedToDashboard = user?.roles.some(role => ['Admin', 'Vendor'].includes(role));

  return (
    <div className="navbar bg-base-100 shadow-sm">
      <div className="flex-1 pl-5">
        {
          auth?.user ?
            (
              useSidebar ? (<SidebarTrigger className="-ml-1" />)
                :
                <>
                  <Link href={route('dashboard')} className=" text-xl"> LaraStore</Link>
                </>
            )
            :
            (<Link href={route('home')} className="btn btn-ghost text-xl"> LaraStore</Link>)

        }
      </div>

      <div className="flex-none">
        <MiniCartDropdown />
        {
          auth?.user ? (
            <div className="dropdown dropdown-end">
              <div tabIndex={0} role="button" className="btn btn-ghost btn-circle avatar">
                <div className="w-10 rounded-full">
                  <img
                    alt="Tailwind CSS Navbar component"
                    src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" />
                </div>
              </div>
              <ul
                tabIndex={0}
                className="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow">
                <li>
                  <Link href={route('profile.edit')} className="justify-between"> Profile <span className="badge">New</span></Link>
                </li>

                <li>
                  {isAllowedToDashboard ? (
                    <a href={route('filament.admin.pages.dashboard')}>Settings</a>
                  ) : (
                    <Link href={route('profile.edit')}>Settings</Link>
                  )}
                </li>
                <li>
                  <Link href={route('logout')} method={"post"} as="button">Logout</Link> </li>

              </ul>
            </div>
          )
            :
            (

              <ul className="menu menu-horizontal px-1 gap-1">
                <li>
                  <Link href={route('login')} as="button" className="btn">
                    Login</Link>
                </li>
                <li><Link href={route('register')} as="button" className="btn btn-primary">
                  Register</Link></li>
              </ul>
            )
        }

      </div>
    </div>
  );
}
