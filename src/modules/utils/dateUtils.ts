export const getFormattedDate = (date: string): string => {
    const dateObject = new Date(date);
    return `${dateObject.toLocaleDateString()} ${dateObject.toLocaleTimeString('pl-PL')}`
}