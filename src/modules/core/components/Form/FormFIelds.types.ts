export type FormFieldProps = {
    name: string,
    value: string | boolean,
    type: "select" | "input",
    onChange: (value: string | boolean) => void;
}