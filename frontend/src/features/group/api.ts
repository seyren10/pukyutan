import { httpClient } from "@/services/axios/axios";
import type { SimplePaginatedResponse } from "@/types/paginate";
import type { CreateGroupSchema, Group, GroupQueryParams } from "./type";

//  api/v1/groups ................................................................................................................................................. groups.index › V1\GroupController@index
//   POST            api/v1/groups ................................................................................................................................................. groups.store › V1\GroupController@store
//   POST            api/v1/groups/join/{invite_code} ............................................................................................................................................... V1\GroupJoinController
//   GET|HEAD        api/v1/groups/{group} ........................................................................................................................................... groups.show › V1\GroupController@show
//   PUT|PATCH       api/v1/groups/{group} ....................................................................................................................................... groups.update › V1\GroupController@update
//   DELETE          api/v1/groups/{group} ..................................................................................................................................... groups.destroy › V1\GroupController@destroy
//   POST            api/v1/groups/{group}/activate ............................................................................................................................................. V1\GroupActivateController
//   GET|HEAD        api/v1/groups/{group}/activities ........................................................................................................................................... V1\GroupActivityController
//   POST            api/v1/groups/{group}/complete ............................................................................................................................................. V1\GroupCompleteController
//   GET|HEAD        api/v1/groups/{group}/members ........................................................................................................................ groups.members.index › V1\MemberController@index
//   POST            api/v1/groups/{group}/members ........................................................................................................................ groups.members.store › V1\MemberController@store
//   POST            api/v1/groups/{group}/rounds .................................................................................................................................................. V1\GroupRoundController
//   GET|HEAD        api/v1/groups/{group}/share-requests ........

export const getGroups = async (params?: GroupQueryParams) => {
  const res = await httpClient.get<SimplePaginatedResponse<Group>>(
    "/api/v1/groups",
    { params },
  );
  return res.data;
};

export const getSharedGroups = async (params?: GroupQueryParams) => {
  const res = await httpClient.get<SimplePaginatedResponse<Group>>(
    "/api/v1/groups/shared",
    { params },
  );
  return res.data;
};

export const createGroup = async (payload: CreateGroupSchema) => {
  const res = await httpClient.post<{ data: Group }>("/api/v1/groups", payload);
  return res.data;
};
