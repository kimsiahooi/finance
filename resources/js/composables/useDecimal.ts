export const useDecimal = () => {
    const formatDecimal = (
        value?: number | string | null,
        locales?: Intl.LocalesArgument,
    ) =>
        value
            ? (+value).toLocaleString(locales, {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2,
              })
            : undefined;

    return { formatDecimal };
};
