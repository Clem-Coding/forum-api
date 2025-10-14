import { ref } from "vue";

export function useFormErrors() {
  const errors = ref<Record<string, string[]>>({});

  const setErrors = (violations: { propertyPath: string; message: string }[] | undefined) => {
    errors.value = {};
    if (Array.isArray(violations)) {
      errors.value = violations.reduce((acc: Record<string, string[]>, err) => {
        if (!acc[err.propertyPath]) acc[err.propertyPath] = [];
        (acc[err.propertyPath] ?? []).push(err.message);
        return acc;
      }, {});
    }
  };

  return { errors, setErrors };
}
