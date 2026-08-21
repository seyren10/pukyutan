export type DashboardStats = {
  groups: {
    active: number;
    draft: number;
    completed: number;
  };
  members_total: number;
  // null when none of the user's active groups currently have an open
  // cycle (e.g. every group is still a draft, or every round is complete).
  current_cycle: {
    collected_total: number;
    expected_total: number;
  } | null;
  // The single soonest-due payout across all of the user's active groups —
  // the dashboard's answer to "what do I need to know about right now?".
  next_payout: {
    group_id: number;
    group_name: string;
    recipient_name: string;
    amount: number;
    due_date: string;
  } | null;
};
