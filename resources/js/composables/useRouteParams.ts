export const useRouteParams = () => {
    const params = new URLSearchParams(window.location.search);

    return { params };
};
