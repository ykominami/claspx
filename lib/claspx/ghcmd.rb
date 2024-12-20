require 'json'

module Claspx
  class GhCmd < Cmd
    def initialize()
      super("gh")
    end

    def list_repository(limit: MAX_PROJECTS)
      cmdline = "#{@name} repo list --limit #{limit}"
      result = execute(cmdline)
      result.add_message_memo("list_repository|cmdline=#{cmdline}")
      return_value = result.std_out
      return_array = return_value.split("\n").map{ |it| it.split("\t") }
      result.add_return_array(return_array)
    end

    def list_repository_name(limit: 300)
      cmdline = "#{@name} repo list --limit #{limit} --json name"
      result = execute(cmdline)
      result.add_message_memo("list_repository_name|cmdline=#{cmdline}")
      return_value = result.std_out
      return_array = JSON.parse(return_value).map{ |item| item["name"] }
      result.add_return_array(return_array)
    end

    def find_remote_repository(repository_name)
      result = list_repository_name()
      return result if result.error?

      # result.return_array.map{ |item| item[0] }.grep(/#{repository_name}/).positive?
      array = result.return_array.grep(/#{repository_name}/)
      array = [] if array.nil?
      ret = array.size.positive?
      result2 = Result.new(ret, "find_remote_repository|ret=#{ret}")
      result2.add_array(array)
      result2.add_return_array(array)
    end

    def create_public_repository(chdir, repository_name)
      result = find_remote_repository(repository_name)

      return result if result.success?

      cmdline = "#{@name} repo create --public -s #{chdir} #{repository_name}"
      execute(cmdline)
    end

    def delete_public_repository(repository_name)
      @timeout = 10
      result = find_remote_repository(repository_name)
      return result if result.error?

      cmd_line = "#{@name} repo delete #{repository_name}"

      result = "NIL"
      result2 = spawn(cmd_line) do | read, write |
        @read = read
        @write = write
      
        @read.expect(/Type(\s+)([^\s]+)(\s+)([^:]+):/, @timeout) do | match |
          confirm_statement = match[2]
          @write.write("#{confirm_statement}\n")
        end

        result = Result.new(true, "spawn cmd_line=#{cmd_line}")
      end
      if result2.nil?
        result
      else
        result2
      end
    end
  end
end
