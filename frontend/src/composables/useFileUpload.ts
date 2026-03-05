import { ref } from "vue";

export function useFileUpload() {
  const fileInput = ref<HTMLInputElement>();

  function onFileChange(event: Event, callback: (dataUrl: string) => void) {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) loadFile(file, callback);
  }

  function onDrop(event: DragEvent, callback: (dataUrl: string) => void) {
    const file = event.dataTransfer?.files?.[0];
    if (file) loadFile(file, callback);
  }

  function onClickSelect() {
    fileInput.value?.click();
  }

  function loadFile(file: File, callback: (dataUrl: string) => void) {
    const reader = new FileReader();
    reader.onload = () => callback(reader.result as string);
    reader.readAsDataURL(file);
  }

  return { fileInput, onFileChange, onDrop, onClickSelect };
}
