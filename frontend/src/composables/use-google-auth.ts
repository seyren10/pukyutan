import { ref } from "vue";

const API_URL = import.meta.env.VITE_API_URL;
const GOOGLE_REDIRECT_URL = import.meta.env.VITE_GOOGLE_REDIRECT;

interface PopupMessage {
  source: "google-auth-popup";
  status: "success" | "error";
  message: string | null;
}

export function useGoogleAuth() {
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  function openPopup(): Window | null {
    const width = 500;
    const height = 600;
    const left = window.screenX + (window.outerWidth - width) / 2;
    const top = window.screenY + (window.outerHeight - height) / 2;

    return window.open(
      GOOGLE_REDIRECT_URL,
      "google-oauth",
      `width=${width},height=${height},left=${left},top=${top}`,
    );
  }

  function signInWithGoogle(): Promise<void> {
    return new Promise((resolve, reject) => {
      error.value = null;
      const popup = openPopup();

      if (!popup) {
        error.value = "Popup blocked. Please allow popups for this site.";
        reject(new Error(error.value));
        return;
      }

      isLoading.value = true;

      function cleanup() {
        isLoading.value = false;
        window.removeEventListener("message", handleMessage);
        window.clearInterval(pollClosed);
      }

      function handleMessage(event: MessageEvent<PopupMessage>) {
        if (event.origin !== API_URL) return;
        if (event.data?.source !== "google-auth-popup") return;

        cleanup();

        if (event.data.status === "success") {
          resolve();
        } else {
          error.value = event.data.message ?? "Google authentication failed.";
          reject(new Error(error.value));
        }
      }

      // Fallback: if the user closes the popup manually without completing auth
      const pollClosed = window.setInterval(() => {
        if (popup.closed) {
          cleanup();
          error.value = error.value ?? "Sign in was cancelled.";
          reject(new Error(error.value));
        }
      }, 500);

      window.addEventListener("message", handleMessage);
    });
  }

  return { isLoading, error, signInWithGoogle };
}
