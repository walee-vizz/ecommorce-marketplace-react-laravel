import { Breadcrumbs } from '@/components/breadcrumbs';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import { Navbar } from './app/navbar';
import toast, { Toaster } from 'react-hot-toast';


interface AppHeaderProps {
  breadcrumbs?: BreadcrumbItem[];
}

export function AppHeader({ breadcrumbs = [] }: AppHeaderProps) {
  const page = usePage<SharedData>();
  const { error, success } = page.props;
  if (error) {
    toast.error(error);
  }
  if (success) {
    toast.success(success);
  }
  return (
    <>
      <Toaster />
      <div className="border-sidebar-border/80 border-b">
        <Navbar useSidebar={false} />
      </div>
      {breadcrumbs.length > 1 && (
        <div className="border-sidebar-border/70 flex w-full border-b">
          <div className="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl">
            <Breadcrumbs breadcrumbs={breadcrumbs} />
          </div>
        </div>
      )}
    </>
  );
}
