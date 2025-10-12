import type { AppPageProps } from '@/types';
import { usePage as useInertiaPage } from '@inertiajs/vue3';

export const usePage = () => {
    const page = useInertiaPage<AppPageProps>();

    return { page };
};
