import { CalendarDate } from '@internationalized/date';

export const useCalendarDate = () => {
    const formatCalendarDate = (date?: Date) =>
        date &&
        new CalendarDate(
            date.getFullYear(),
            date.getMonth() + 1,
            date.getDate(),
        );

    return { formatCalendarDate };
};
