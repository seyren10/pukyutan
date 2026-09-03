import { useMutation } from "@tanstack/vue-query";
import { generateAndDownloadMemberLedgerPdf } from "../api";
import type { DownloadMemberLedgerPdfPayload } from "../type";
import type { LaravelError } from "@/types/common";
import { toast } from "vue-sonner";

export const useDownloadMemberLedgerPdfMutation = () => {
  const { mutate, isPending } = useMutation({
    mutationFn: (payload: DownloadMemberLedgerPdfPayload) =>
      generateAndDownloadMemberLedgerPdf(payload),
    onMutate: () => {
      const toastId = toast.loading("Generating ledger PDF…");

      return { toastId };
    },
    onSuccess: (_data, _variables, context) => {
      toast.success("Ledger PDF downloaded", { id: context?.toastId });
    },
    onError: (error: LaravelError | Error, _variables, context) => {
      const message =
        "response" in error
          ? error.response?.data?.message
          : error.message;

      toast.error(message ?? "Could not generate the PDF. Please try again.", {
        id: context?.toastId,
      });
    },
  });

  return {
    mutate,
    isPending,
  };
};