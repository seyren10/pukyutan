import { useClipboard } from "@vueuse/core";
import { toast } from "vue-sonner";

export const useCopyInviteCode = () => {
  // `copied` flips back to false automatically after the default 1500ms,
  // matching the manual setTimeout this composable replaces.
  const { copy, copied } = useClipboard();

  const copyInviteCode = async (inviteCode?: string | null) => {
    if (!inviteCode) return;

    await copy(inviteCode);
    toast.success("Invite code copied");
  };

  return { copyInviteCode, copied };
};
