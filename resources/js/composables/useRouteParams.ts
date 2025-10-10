import queryString from 'query-string';

export const useRouteParams = () => {
    const params = <Record<string, string>>queryString.parse(
        window.location.search,
        {
            arrayFormat: 'index',
        },
    );

    return { params };
};
