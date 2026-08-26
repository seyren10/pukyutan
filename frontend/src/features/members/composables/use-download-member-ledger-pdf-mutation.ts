import { useMutation } from "@tanstack/vue-query";
import { downloadMemberLedgerPdf } from "../api";
import type { DownloadMemberLedgerPdfPayload } from "../type";
import { toast } from "vue-sonner";

export const useDownloadMemberLedgerPdfMutation = () => {
  const { mutate, isPending } = useMutation({
    mutationFn: (payload: DownloadMemberLedgerPdfPayload) =>
      downloadMemberLedgerPdf(payload),
    onError: () => toast.info("Could not generate the PDF. Please try again."),
  });

  return {
    mutate,
    isPending,
  };
};
