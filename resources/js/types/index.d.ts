import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    currentEvent: Event;
    events: Event[];
    auth: Auth;
    sidebarOpen: boolean;
};

type Role = 'admin' | 'super admin' | 'contributor' | 'viewer';
export type QuestionType = 'mcq' | 'true_false' | 'short_answer' | 'long_answer';

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    roles: Role[];
    isSuperAdmin: boolean;
    canManageSubject: boolean;
    canAddQuestion: boolean;
    canManageQuestion: boolean;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface PaginatedUser {
    data?: User[];
    links?: {
        first: string | null;
        last: string | null;
        next: string | null;
        prev: string | null;
    };
    meta?: {
        current_page: number;
        from: number;
        last_page: number;
        links: [
            {
                active: boolean;
                label: string;
                page: number | null;
                url: string | null;
            },
        ];
        path: string;
        per_page: number;
        to: number;
        total: number;
    };
}

export interface Subject {
    id: number;
    name: string;
    description: string;
    questionCount: number;
    topics: Topic[];
    questions: Question[];
    createdAt: string;
    updatedAt: string;
}

export interface Topic {
    id: number;
    name: string;
    subject: Subject;
    description?: string;
    questionCount?: number;
}

export interface Event {
    id: number;
    name: string;
    startDate: string;
    endDate: string;
}

export interface Question {
    id: number;
    questionText: string;
    difficulty: 'easy' | 'medium' | 'hard';
    status: 'pending' | 'approved' | 'rejected';
    round: number;
    type: string;
    subject: Subject;
    topic: Topic;
    answer: Answer;
    boolAnswer: boolean;
    options: QuestionOptions[];
    author: User;
    createdAt?: string;
    createdAtHuman?: string;
    updatedAt?: string;
    updatedAtHuman?: string;
    lastModifier?: User;
    hasBeenAsked: boolean;
}

export interface Session {
    id: number;
    title: string;
    difficulty: string;
    type: string;
    round: number;
    questions: Question[];
    startsAt: string;
    endsAt: string;
    status: 'pending' | 'ongoing' | 'ended';
}

export interface ExportFilters {
    subject: string;
    topic: string;
    difficulty: string;
    round: string;
    status: string;
    type: string;
    dateRange: string;
}

export interface ExportSettings {
    includeAnswers: boolean;
    includeExplanations: boolean;
    includeMetadata: boolean;
    randomizeOrder: boolean;
}

export interface ExportRecord {
    id: number;
    date: string;
    format: string;
    questionCount: number;
    filters: string;
}

export interface PaginatedQuestion {
    data?: Question[];
    links?: {
        first: string | null;
        last: string | null;
        next: string | null;
        prev: string | null;
    };
    meta?: {
        current_page: number;
        from: number;
        last_page: number;
        links: [
            {
                active: boolean;
                label: string;
                page: number | null;
                url: string | null;
            },
        ];
        path: string;
        per_page: number;
        to: number;
        total: number;
    };
}

export interface QuestionOptions {
    id: number;
    optionText: string;
    isCorrect: boolean;
}

export interface Answer {
    id: number;
    answerText: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export type NavLinkType = {
    label: string;
    icon: any;
    link: any;
    visible?: boolean
};


export interface Activity{
    id: number,
    title: string,
    description?: string,
    type: string,
    createdAt: string
}