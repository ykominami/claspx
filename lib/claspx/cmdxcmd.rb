require 'pty'
require 'expect'
require 'timeout'

# require 'open3'

module Claspx
  class CmdxCmd < Cmd
    def initialize()
      super("bin/cmdx")
    end

    def x()
      @timeout = 10
      which_cmd = WhichCmd.new
      result = which_cmd.which("gh")
      return result if result.error?

      gh_path = result.return_value.chomp

      repository_name = "ykominami/__test_project"
      cmd_line = "#{@name} repo delete #{repository_name}"

      ret = "NIL"
      ret2 = spawn(cmd_line) do | read, write |
        @read = read
        @write = write
      
        @read.expect(/Type(\s+)([^\s]+)(\s+)([^:]+):/, @timeout)do | match |
          confirm_statement = match[2]
          @write.write("#{confirm_statement}\n")
        end

        ret = Result.new(true, "xxxx cmd_line=#{cmd_line}")
      end 
      if ret2.nil?
        ret
      else
        ret2
      end 
    end

    def xx(cmd_line, &block)
      if block_given?
        block.call(cmd_line)
      end
    end

    def z()
      GhCmd.new.find_remote_repository("asdf")
    end

    def list_repository_name(limit: 300)
      cmdline = "#{@name}_repo_list repo list --limit #{limit} --json name"
      result = execute(cmdline)
      result.add_message_memo("list_repository|cmdline=#{cmdline}")
      return_value = result.std_out
      return_array = JSON.parse(return_value).map{ |item| item["name"] }
      result.add_return_array(return_array)
    end

    def find_remote_repository(repository_name)
      result = list_repository_name()
      return result if result.error?

      # result.return_array.map{ |item| item[0] }.grep(/#{repository_name}/).positive?
      array = result.return_array.grep(/#{repository_name}/)
      ret = array.size.positive?
      result = Result.new(ret, "find_remote_repository|ret=#{ret}")
      result.add_array(array)
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

      ret = "NIL"
      ret2 = spawn(cmd_line) do | read, write |
        @read = read
        @write = write
      
        @read.expect(/Type(\s+)([^\s]+)(\s+)([^:]+):/, @timeout) do | match |
          confirm_statement = match[2]
          @write.write("#{confirm_statement}\n")
        end

        ret = Result.new(true, "spawn cmd_line=#{cmd_line}")
      end
      ret2.nil? ? ret : ret2
    end
  end
end
