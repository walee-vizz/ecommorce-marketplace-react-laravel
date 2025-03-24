import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    roles: string[]; // Array of role names (e.g., ['admin', 'user'])
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

export type Product = {
    id: number;
    title: string;
    slug: string;
    description: string;
    price: number;
    stock: number;
    image: string;
    user:{
        id: number;
        name: string;
    };
    department: {
        id: number;
        name: string;
    };
    category: {
        id: number;
        name: string;
    };
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
}

export interface PaginationLink {
    url: string;
    label: string;
    active: boolean;
};
export interface PaginationMetadata {
    current_page: number;
    last_page: number;
    from: number;
    to: number;
    total: number;
    per_page: number;
    count: number;
    links: PaginationLink[]
}


export type PaginationProps<T> = {
    data:Array<T>;
}
